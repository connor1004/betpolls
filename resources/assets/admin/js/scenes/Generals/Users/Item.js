import React, { Component } from 'react';
import {
  Icon, Card, Stack,
  Button, ButtonGroup,
  ResourceList, SkeletonBodyText
} from '@shopify/polaris';

import {
  COUNTRIES_OPTIONS
} from '../../../configs/options';
import OptionsHelper from '../../../helpers/OptionsHelper';


class Item extends Component {
  constructor(props) {
    super(props);
    this.state = {
      isDeleting: false,
      showDeleteConfirm: false
    };
    this.formikRef = null;
    this.toggleDeleteConfirm = this.toggleDeleteConfirm.bind(this);
    this.handleToggleActive = this.handleToggleActive.bind(this);
    this.handleToggleConfirmed = this.handleToggleConfirmed.bind(this);
    this.handleDetail = this.handleDetail.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
  }

  setDeleting(isDeleting) {
    this.setState({
      isDeleting
    });
  }

  toggleDeleteConfirm() {
    this.setState({
      showDeleteConfirm: !this.state.showDeleteConfirm
    });
  }

  async handleToggleActive() {
    const {
      onToggleActive, data
    } = this.props;
    await onToggleActive(data.id, this);
  }

  async handleToggleConfirmed() {
    const {
      onToggleConfirmed, data
    } = this.props;
    await onToggleConfirmed(data, this);
  }

  handleDetail() {
    const {
      data, onDetail
    } = this.props;
    onDetail(data);
  }

  async handleDelete() {
    const {
      onDelete, data
    } = this.props;
    await onDelete(data.id, this);
  }

  renderViewMode() {
    const {
      data
    } = this.props;
    const {
      showDeleteConfirm, isDeleting
    } = this.state;

    const contry = OptionsHelper.getItem(data.country, 'code', COUNTRIES_OPTIONS);

    return (
      <div>
        <Card>
          <ResourceList.Item>
            {
              showDeleteConfirm ? (
                <Stack>
                  <Stack.Item fill>
                    {`Do you want to delete ${data.firstname}?`}
                  </Stack.Item>
                  <Stack.Item>
                    <ButtonGroup segmented>
                      <Button
                        primary
                        loading={isDeleting}
                        onClick={this.handleDelete}
                      >
                        Yes
                      </Button>
                      <Button
                        disabled={isDeleting}
                        onClick={this.toggleDeleteConfirm}
                      >
                        No
                      </Button>
                    </ButtonGroup>
                  </Stack.Item>
                </Stack>
              ) : (
                <Stack>
              <Stack.Item>
                <div style={{ width: '50px' }}>{data.id}</div>
              </Stack.Item>
              <Stack.Item>
                <div style={{ width: '180px' }}>{`${data.firstname} ${data.lastname}`}</div>
              </Stack.Item>
              <Stack.Item>
                <div style={{ width: '160px' }}>{data.username}</div>
              </Stack.Item>
              <Stack.Item>
                <div style={{ width: '240px' }}>{data.email}</div>
              </Stack.Item>
              <Stack.Item fill>
                <div style={{ width: '240px' }}>{contry && contry.name}</div>
              </Stack.Item>
              <Stack.Item fill>
                <div style={{ width: '80px' }}>
                  {
                    data.robot
                      ? <span className="Polaris-Button__Content">
                          <Icon source='checkmark' /> <span>Robot</span>
                        </span>
                      : ''
                  }
                </div>
              </Stack.Item>
              <Stack.Item fill>
                <div style={{ width: '80px' }}>{data.created_at}</div>
              </Stack.Item>
              <Stack.Item>
                <div style={{ width: '200px' }}>
                  <ButtonGroup segmented>
                    <Button
                      primary
                      icon="products"
                      onClick={this.handleDetail}
                    />
                    <Button
                      primary={data.confirmed === 1}
                      destructive={data.confirmed === 0}
                      onClick={this.handleToggleConfirmed}
                    >
                      {data.confirmed ? 'Confirmed' : 'Uncomfirmed'}
                    </Button>
                    <Button
                      icon={data.deleted_at === null ? 'cancelSmall' : 'checkmark'}
                      primary={data.deleted_at !== null}
                      destructive={data.deleted_at === null}
                      onClick={this.handleToggleActive}
                    />
                    <Button
                      destructive
                      icon="delete"
                      onClick={this.toggleDeleteConfirm}
                    />
                  </ButtonGroup>
                </div>
              </Stack.Item>
            </Stack>
              )
            }
          </ResourceList.Item>
        </Card>
      </div>
    );
  }

  render() {
    const {
      isLoading
    } = this.props;
    if (isLoading) {
      return (
        <Card>
          <ResourceList.Item>
            <SkeletonBodyText />
          </ResourceList.Item>
        </Card>
      );
    }
    return this.renderViewMode();
  }
}

Item.defaultProps = {
  isLoading: false,
  onToggleConfirmed: () => {},
  onToggleActive: () => {},
  onDetail: () => {}
};

export default Item;
